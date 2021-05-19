# PHPattern
Simple pattern creator and EIP offset finder for buffer overflow PoC.

This will generate a big string that will be sent to the software to be exploited. In the best situation, the program will crash (with a segmentation fault or something like that) and you'll be able to see the value of EIP with a debugger. With this value in hand, simply use the "find EIP" feature to find the size of the string you need to send in order to hit EIP. Knowing this size, you can manipulate EIP and change the program flow to jump to some place, run some shellcode, etc.

# Usage

### Generate pattern

```
$ ./PHPattern fuzz <size>
Returns a special string to be used as 'buffer filler' to hit EIP
size = Length of the string to be generated
Max size: 3.767.680. Use size = 0 to max size.
```

Ex:
```
$ ./PHPattern fuzz 100
AA10AA11AA12AA13AA14AA15AA16AA17AA18AA19AA20AA21AA22AA23AA24AA25AA26AA27AA28AA29AA30AA31AA32AA33AA34
```

### Find EIP

```
$ ./PHPattern find <offset> [size]
Returns the length of the buffer that needs to be sent to hit EIP
offset = The value of EIP after the program crashed
size = The size of the string used to crash (faster, but optional)
```

Ex:
```
$ ./PHPattern find 46413136
[+] You need 2006 bytes to reach EIP.
```

## Example

I used [Vulnserver](https://github.com/stephenbradshaw/vulnserver) to test PHPattern.

![image](https://user-images.githubusercontent.com/3837916/118740628-b7623e80-b822-11eb-92f4-a147b4bd0054.png)
Generating the pattern to send

![image](https://user-images.githubusercontent.com/3837916/118740906-66067f00-b823-11eb-93d9-3b26ea7c3ab3.png)
Pattern was sent and program crashed. Note the EIP value on the right.

![image](https://user-images.githubusercontent.com/3837916/118741323-63585980-b824-11eb-9b6e-028fffc61801.png)
1. Got the EIP value
2. PHPattern was used to find the size of the buffer to reach EIP
3. The obtained size was set
4. EIP was set to a known value (ABCD, 41424344 in hex)
5. Program crashed and our value was set to EIP (44434241 in little endian)
